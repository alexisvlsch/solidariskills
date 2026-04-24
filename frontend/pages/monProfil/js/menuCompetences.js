// Gère la gestion des compétences utilisateur
let userCompetences = [];
let selectedCompetenceIds = [];

document.addEventListener('DOMContentLoaded', function() {
  fetch('../../../backend/competence/getUserCompetences.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        selectedCompetenceIds = data.competences.map(comp => comp.id_competence);
        userCompetences = data.competences.map(comp => comp.nom);
        updateSelectedCompetencesDisplay();
      }
    })
    .catch(error => console.error('Erreur lors de la récupération des compétences:', error));
  const themeSelect = document.getElementById('theme-select');
  if (themeSelect) {
    themeSelect.addEventListener('change', function() {
      loadCompetencesForTheme(this.value);
    });
  }
});

function openCompetencesPopup() {
  document.getElementById('competencesPopup').style.display = 'flex';
  const themeSelect = document.getElementById('theme-select');
  loadCompetencesForTheme(themeSelect.value);
}

function closeCompetencesPopup() {
  document.getElementById('competencesPopup').style.display = 'none';
}

function loadCompetencesForTheme(theme) {
  const competencesList = document.getElementById('competences-list');
  competencesList.innerHTML = '<p>Chargement...</p>';
  fetch(`../../../backend/competence/get_competences.php?categorie=${theme}`)
    .then(response => response.json())
    .then(competences => {
      if (competences.length === 0) {
        competencesList.innerHTML = '<p>Aucune compétence disponible pour cette catégorie</p>';
        return;
      }
      competencesList.innerHTML = '';
      competences.forEach(comp => {
        const isSelected = selectedCompetenceIds.includes(parseInt(comp.id_competence));
        const checkbox = document.createElement('div');
        checkbox.className = 'competence-item';
        checkbox.innerHTML = `
          <label>
            <input type="checkbox" value="${comp.id_competence}" 
              ${isSelected ? 'checked' : ''} 
              onchange="toggleCompetence(${comp.id_competence}, '${comp.nom}', this.checked)">
            ${comp.nom}
          </label>
        `;
        competencesList.appendChild(checkbox);
      });
    })
    .catch(error => {
      console.error('Erreur:', error);
      competencesList.innerHTML = '<p>Erreur lors du chargement des compétences</p>';
    });
}

function toggleCompetence(id, name, isChecked) {
  // Ajoute ou retire une compétence sélectionnée
  if (isChecked) {
    if (!selectedCompetenceIds.includes(id)) {
      selectedCompetenceIds.push(id);
    }
  } else {
    const index = selectedCompetenceIds.indexOf(id);
    if (index !== -1) {
      selectedCompetenceIds.splice(index, 1);
    }
  }
  updateSelectedCompetencesDisplay();
}

function updateSelectedCompetencesDisplay() {
  // Met à jour l'affichage des compétences sélectionnées
  const container = document.getElementById('selected-competences-container');
  container.innerHTML = '';
  if (selectedCompetenceIds.length === 0) {
    container.innerHTML = '<p>Aucune compétence sélectionnée</p>';
    return;
  }
  const form = document.querySelector('.profile-form');
  let hiddenInput = form.querySelector('input[name="selected_competences"]');
  if (!hiddenInput) {
    hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'selected_competences';
    form.appendChild(hiddenInput);
  }
  hiddenInput.value = JSON.stringify(selectedCompetenceIds);
  fetch('../../../backend/competence/getSelectedCompetences.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ competenceIds: selectedCompetenceIds })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const competencesList = data.competences;
      container.innerHTML = '';
      competencesList.forEach(comp => {
        const tag = document.createElement('span');
        tag.className = 'competence-tag';
        tag.innerHTML = `
          ${comp.nom}
          <span class="remove-competence" onclick="toggleCompetence(${comp.id_competence}, '${comp.nom}', false)">×</span>
        `;
        container.appendChild(tag);
      });
      document.getElementById('competences-display').value = competencesList.map(comp => comp.nom).join(', ');
    }
  })
  .catch(error => console.error('Erreur:', error));
}

function saveCompetences() {
  // Enregistre les compétences sélectionnées côté serveur
  fetch('../../../backend/competence/updateUserCompetences.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ competenceIds: selectedCompetenceIds })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      closeCompetencesPopup();
      document.getElementById('competences-display').value = data.competences.join(', ');
    } else {
      alert('Erreur lors de la mise à jour des compétences: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Erreur:', error);
    alert('Erreur lors de la mise à jour des compétences');
  });
}
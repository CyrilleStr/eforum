function onClickModifyAccountBtn(event) {
  console.log("onClickModifyAccountBtn function");
  event.preventDefault();
  const div = document.getElementById("account_infos");            
  div.innerHTML = `<form id=\"modify_form\" action=\"/user/my_account/modify\"><h1>Prénom</h1><input id=\"firstNameInput\" value=\"${userFirstName}\" type=\"text\" name=\"firstName\" required=\"required\" maxlength=\"255\"><h1>Nom</h1><input id=\"lastNameInput\" value=\"${userLastName}\" type=\"text\" name=\"lastName\" required=\"required\" maxlength=\"255\"><h1>Email</h1><input id=\"emailInput\" value=\"${userEmail}\" type=\"text\" name=\"email\" required=\"required\" maxlength=\"255\"><h1>Activité</h1><textarea id=\"activityInput\"  type=\"text\" name=\"acitvity\" required=\"required\">${userActivity}</textarea><br><br><button id=\"submit\" type=\"submit\" class=\"btn-primary\">Enregister</button></form>`
  
  div.querySelector("#submit").addEventListener('click',onclickSubmitButton);
}

function onclickSubmitButton(event){
  console.log("onclicksubmitbutton function");
  event.preventDefault();
  const form =  document.querySelector('#modify_form');
  const firstNameInput = form.querySelector('#firstNameInput').value;
  const lastNameInput = form.querySelector('#lastNameInput').value;
  const emailInput = form.querySelector('#emailInput').value;
  const activityInput = form.querySelector('#activityInput').value;
  const button = this;

  // Interrupting input

  button.innerHTML = "Modification...";
  button.disabled = true;

  // Extracting and packaging data  
  const data = new FormData();
  data.append('firstName',firstNameInput);
  data.append('lastName',lastNameInput);
  data.append('email',emailInput);
  data.append('activity',activityInput);

  // Sending data
  const url = form.action;
  const requestAjax = new XMLHttpRequest();
  requestAjax.onreadystatechange = responseManager;
  requestAjax.open('POST',url);
  requestAjax.send(data);
  function responseManager () {
    console.log("responseManager function");
    if (requestAjax.readyState === XMLHttpRequest.DONE) {
      if (requestAjax.status === 200) {
        const div = document.getElementById("account_infos");
        div.innerHTML = `
        <h1 class="display-3">${firstNameInput} ${lastNameInput}</h1>
        <p class="lead">Membre depuis le ${accountCreationDate} </p>
        <hr class="my-4"> 
        <p>${activityInput}</p>
        <button id="modify_account" class="btn-primary">Modifier</button>`
        document.getElementById("modify_account").addEventListener('click',onClickModifyAccountBtn);
      } else {
        form.innerHTML = "Erreur, veuillez actualiser la page";
      }
    }
  }
}

const userFirstName = document.getElementById("userFirstName").value;
const userLastName = document.getElementById("userLastName").value;
const userEmail = document.getElementById("userEmail").value;
const userActivity = document.getElementById("userActivity").value;
const accountCreationDate = document.getElementById("accountCreationDate").value;

document.getElementById("modify_account").addEventListener('click',onClickModifyAccountBtn);

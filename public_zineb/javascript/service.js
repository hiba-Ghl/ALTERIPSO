let orderIn = [];
let input = [];
let input1 = [];
// Créer un objet pour enregistrer les images, comme ça je peux supprimer une image et ajouter une image simplement.
const dataTransfer = new DataTransfer();
// La fonction pour la vérification du formulaire, 
// qui contrôle si le nom ou un autre champ est laissé vide, 
// comprend également une validation d'URL. Elle détermine si l'entrée est une URL valide ou non et affiche l'erreur juste en dessous du champ concerné.
function validateForm(e) {
  const name = document.getElementById("nom_service");
  const url = document.getElementById("service_src");
  const type = document.getElementById("service_type");
  const errorName = document.getElementById("divNom");
  const errorFile = document.getElementById("divFile");
  const DivImageD = document.querySelectorAll(".DivImageD");
  const pNew = document.getElementById("pNew");

  name.style.border = "";
  url.style.border = "";
  input = [];
  for (const div of DivImageD) {
    const orderInput = div.querySelector('input[name^="numberInput"]');
    filterInput(orderInput);
  }
  const duplicates = input.filter(
    (item, index) => input.indexOf(item) !== index && !isNaN(item)
  );
  if (duplicates.length > 0) {
    alert(
      "Cet ordre d'affichage est dupliqué : indiquez des chiffres distincts."
    );
    for (const div of DivImageD) {
      const orderInput = div.querySelector('input[name^="numberInput"]');
      supprimerduplicateValue(orderInput, duplicates);
    }
    return false;
  }
  const orderInfilter = orderIn.some((element) => {
    return element > orderIn.length});
  if(orderInfilter)
  {
    alert("Cet ordre d'affichage n'est pas acceptable et n'est pas trié.");
    return false;
  }
  if (document.getElementById("name") || document.getElementById("url"))
    return false;
  if (name.value.trim() === "") {
    name.style.border = "2px solid red";
    const p = document.createElement("p");
    p.textContent = "Le Nom du service est requi";
    p.style.color = "red";
    p.setAttribute("id", "name");
    errorName.appendChild(p);

    return false;
  }
  if (type.value === "URL" && !isValidUrl(url.value)) {
    url.style.border = "2px solid red";
    const p = document.createElement("p");
    p.textContent = "Veuillez fournir une URL valide.";
    p.style.color = "red";
    p.setAttribute("id", "url");
    errorFile.appendChild(p);
    return false;
  }
  if (type.value === "DIAPO") {
    if (DivImageD.length === 0) {
      alert("Entrez des éléments pour créer un diapo.");
      return false;
    }
    for (const div of DivImageD) {
      const timeInput = div.querySelector('input[name^="timeInput"]');
      const orderInput = div.querySelector('input[name^="numberInput"]');
      if (
        !timeInput ||
        !timeInput.value.trim() ||
        !orderInput ||
        !orderInput.value.trim()
      ) {
        alert("Veuillez indiquer l'ordre et la durée d'affichage des images.");
        return false;
      }
    }
  }

  if (type.value != "URL" && type.value != "DIAPO") {
    if (
      (url.files.length === 0 && e === true) ||
      (e === false && (pNew === null || pNew === undefined))
    ) {
      url.style.border = "2px solid red";
      const p = document.createElement("p");
      p.textContent = "Inclure un fichier est requis.";
      p.style.color = "red";
      p.setAttribute("id", "url");
      errorFile.appendChild(p);
      return false;
    }
  }
  if (url.files.length > 0) {
    for (let i = 0; i < url.files.length; i++) {
      const fsize = url.files.item(i).size;
      const file = Math.round(fsize / 1024);
      if (file >= 8192 && type.value === "PDF") {
        url.style.border = "2px solid red";
        const p = document.createElement("p");
        p.textContent =
          "Fichier trop grand, veuillez sélectionner un fichier de moins de 8 Mb.";
        p.style.color = "red";
        p.setAttribute("id", "url");
        errorFile.appendChild(p);
        return false;
      } else if (file >= 800 && type.value === "IMAGE") {
        url.style.border = "2px solid red";
        const p = document.createElement("p");
        p.textContent =
          "Fichier trop grand, veuillez sélectionner un fichier de moins de 800 kb";
        p.style.color = "red";
        p.setAttribute("id", "url");
        errorFile.appendChild(p);
        return false;
      }
    }
    return true;
  }
  return true;
}

// verifier l'input de URL est ce que un Url ou non
function isValidUrl(value) {
  try {
    new URL(value);
    return true;
  } catch (_) {
    return false;
  }
}

// afficher les options de position de chaque service.
async function addPositions(serviceId, set,value,oldValue,NewValue) {
  try {
    const response = await fetch(`/services/${serviceId}`);
    const data = await response.json();
    set_Position(data, set,value,oldValue,NewValue);
  } catch (error) {
    console.error("Error fetching service data:", error);
  }
}

// La fonction de filtrage des positions sélectionnées auparavant ne figure pas parmi les options affichées.
function set_Position(data, set,value,oldValue,NewValue) {
  const select = document.getElementById("service_position");
  if (select) {
    if (set) {
      while (select.children.length > 1) {
        select.removeChild(select.lastChild);
      }
    } else
    while (select.children.length > 0) {
      select.removeChild(select.lastChild);
    }
  }
  function findDifferences(array1, array2) {
    const diff1 = array1.filter((item) => !array2.includes(item));
    const diff2 = array2.filter((item) => !array1.includes(item));
    return [...diff1, ...diff2];
  }
  
  const choices = Array.from({ length: 50 }, (_, i) => i + 1);
  let result = findDifferences(data, choices);
  if(oldValue === NewValue)
  {
    const val = parseInt(value,10);
    if(!isNaN(val))
      result.push(val);
  }
  result.sort(function(a, b) {
    return a - b;
  });;
  const uniqueResult = [...new Set(result)];
  uniqueResult.forEach((optionData) => {
    const option = document.createElement("option");
    if (optionData == value && oldValue === NewValue) {
      option.setAttribute("selected","selected");
    }
      option.value = optionData;
      option.textContent = optionData;
      select.appendChild(option);
  });
}

// Cette fonction  sélectionne une image et l’ajoute  comme input dans le formulaire  
function selectImage(imagePath) {
  const div = document.getElementById("img");
  const thumbnails = document.querySelectorAll(".image-thumbnail");
  thumbnails.forEach((img) => {
    img.classList.remove("selected")
  });
  
  let clickedImage = Array.from(thumbnails).find((img) =>
  {
    const imgPath = decodeURIComponent(new URL(img.src, window.location.origin).pathname);
    const targetPath = decodeURIComponent("/" + imagePath);
    return imgPath === targetPath;

  }
  );
  if (clickedImage || imagePath) {
    let source = "";
    if(!clickedImage)
    {
      source = '/images/no_image.png';
    }
    else 
      source = clickedImage.src;
    fetch(source)
      .then((res) => res.blob())
      .then((blob) => {
        const fileName = imagePath.split("/").pop();
        const file = new File([blob], fileName, { type: blob.type });

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const existingInput = document.getElementById("inputTransferData1");
        if (existingInput) existingInput.remove();

        const input = document.createElement("input");
        input.type = "file";
        input.name = "ajout_logo1";
        input.style.display = "none";
        input.id = "inputTransferData1";
        input.files = dataTransfer.files;
        
        const form = document.querySelector("form");
        if (form) {
          form.appendChild(input);
        } else {
          console.error(
            "Form element not found. Ensure the form is present in the DOM."
          );
        }
        
        div.src = source;
        $('#exampleModal').modal('hide');
      })
      .catch((error) => {
        console.error("Error fetching image:", error);
      });
  } else {
    console.error("Clicked image not found. Ensure the image path is correct.");
  }
}

// la fonction pour faire la traduction automatique du nom de service
async function translate(text, targetLang, sourceLang) {
  //crée un script via AppScript sur Google et affiche une URL comprenant Body contenant du texte et les éléments de traduction.
  const url =
    "https://script.google.com/macros/s/AKfycbzHo20ZIdkj8KEEf7tAFL74VB6BGvjMERKzKjh7dZ_qFwM6UFAs9YAZTipnumuFXqf1Ow/exec";
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        text: text,
        target_lang: targetLang,
        source_lang: sourceLang,
      }),
    });
    const data = await response.json();
    if (data.status === "success") {
      return data.translatedText;
    } else {
      return null
    }
}

// la fonction pour supprimer les éléments de diapo quand je sélectionne un autre type.
function handleErrorAndReset(errorUrl, service_src) {
  if (errorUrl) {
    errorUrl.remove();
    service_src.style.border = "";
  }
  const add = document.getElementById("dia");
  if (add) add.remove();
}

// fonction pour crée les éléments de type Diapo
function createDiapoInfo() {
  const newElement = document.createElement("div");
  newElement.id = "dia";
  newElement.classList.add("dia");

  const p1 = document.createElement("p");
  p1.textContent =
    "Poids d'un fichier < 5Mo & Poids de tous les fichiers uploadés < 500Mo Résolution : 1920 x 1080 pour (hd)";
  p1.classList.add("red");

  const p2 = document.createElement("p");
  p2.textContent =
    "La fonction DIAPO permet de sélectionner plusieurs fichiers et d'afficher l'ensemble de ces fichiers sur l'écran de la TV.";

  const p3 = document.createElement("p");
  p3.textContent = "Pour paramétrer votre diapo merci de :";

  const p4 = document.createElement("p");
  p4.textContent = "1- enregistrer ce nouveau service";
  p4.classList.add("red");

  const p5 = document.createElement("p");
  p5.textContent = "2- cliquer sur l'icone pour modifier et paramétrer";
  p5.classList.add("red");

  newElement.appendChild(p1);
  newElement.appendChild(p2);
  // newElement.appendChild(p3);
  // newElement.appendChild(p4);
  // newElement.appendChild(p5);

  return newElement;
}
////////////////////////////////////////////////////////////////////////////// 
function changeValeurOrder(element,index,e){
    const order = element.value;
    if(order)
    {
      orderIn[index] = parseInt(order,10);
    }
}
// Fonction pour télécharger les fichiers pour le type Diapo et afficher les sous-inputs.
function getFiles(evt) {
  const files = evt.target.files;
  const displayArea = document.getElementById("divFile");
  let divAll = document.getElementById("divAll");
  if (!divAll) {
    divAll = document.createElement("div");
    divAll.setAttribute("id", "divAll");
  }
  const form = document.getElementById("form");
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    dataTransfer.items.add(file);

    const reader = new FileReader();
    reader.onload = function (e) {
      const index = divAll.childElementCount;
      const DivImg = document.createElement("div");
      DivImg.setAttribute("id", `DivImageD-${index}`);
      DivImg.setAttribute("class", "DivImageD");
      DivImg.innerHTML = `
      <p style="width: 100%; text-align: end; cursor: pointer; color: red;" 
      class="btn btn-xs" 
      onclick="deleteNewIm(${index})" 
      data-toggle="tooltip" 
      data-placement="top" 
      title="Supprimer l'image">
      <i class="glyphicon glyphicon-trash" style="color:red"></i>
      </p>
      `;
                  
                  const img = document.createElement("img");
                  img.src = e.target.result;
                  img.setAttribute("class", "img1");
                  const divInputs = document.createElement("div");
                  divInputs.style.display = "flex";
                  divInputs.style.flexDirection = "column";
                  divInputs.innerHTML = `
                  <label>Ordre d'affichage:</label>
                  <input id="numberInput-${index}" 
                  name="numberInput[${index}]" 
                  class="numberInput"
                  type="number" 
                  min="1"
                  value="${index + 1}"
                  style="width: 100%; margin-bottom: 10px;" onchange = "changeValeurOrder(this,${index})" >
                  <label>Durée d'affichage:</label>
                  <div style="position:relative">
                  <input id="timeInput-${index}" 
                  name="timeInput[${index}]" 
                  type="number" 
                  class="timeInput"
                  min="1"
                  value='5'
                  style="width: 100%; margin-bottom: 10px;">
                  <span style="position:absolute;right:4px;top:2px">(Seconde)</span>
                  </div>
                  `;
                  
                  orderIn.push(index + 1);
                  DivImg.appendChild(img);
                  DivImg.appendChild(divInputs);
                  divAll.appendChild(DivImg);
                };
                reader.readAsDataURL(file);
              }
              const inputTransferData = document.getElementById("inputTransferData");
              if (inputTransferData) inputTransferData.remove();
              const input = document.createElement("input");
              input.type = "file";
              input.name = "service_src1[]";
              input.style.display = "none";
              input.setAttribute("id", "inputTransferData");
              input.files = dataTransfer.files;
              form.appendChild(input);
              displayArea.appendChild(divAll);
            }
            
// Vérifier que le numéro que j'ajoute pour l'ordre d'affichage est celui qui est sélectionné avant.
function filterInput(element) {
  const value = parseInt(element.value, 10);
  input.push(value);
}

function supprimerduplicateValue(element, duplicates) {
  const value = parseInt(element.value, 10);
  if (duplicates.includes(value)) {
    element.style.border = "1px solid red";
  }
}

//La fonction pour supprimer l'image avant d'enregistrer sur la base de donnée,avant de confirmer le formulaire.
async function deleteNewIm(index) {
  const confirmation = confirm(
    `Êtes-vous sûr de vouloir supprimer l’image à l’indice : ${index}?`
  );
  if (!confirmation) return;
  const deleted = document.getElementById(`DivImageD-${index}`);
  const orderInput = document.getElementById(`numberInput-${index}`);
  input = remEt(input, orderInput.value);
  orderIn = remEt(orderIn, orderIn[ orderIn.length - 1]);
  if (deleted) {
    const items = dataTransfer.items;
    for (let i = 0; i < items.length; i++) {
      if (i === index) {
        items.remove(i);
        break;
      }
    }
    const inputTransferData = document.getElementById("inputTransferData");
    if (inputTransferData) {
      inputTransferData.files = dataTransfer.files;
    }
    deleted.remove();
    const divImages = document.querySelectorAll(".DivImageD");
    divImages.forEach((div, i) => {
      div.setAttribute("id", `DivImageD-${i}`);
      const deleteButton = div.querySelector("p");
      deleteButton.setAttribute("onclick", `deleteNewIm(${i})`);

      const numberInput = div.querySelector('input[name^="numberInput"]');
      numberInput.setAttribute("id", `numberInput-${i}`);
      numberInput.setAttribute("name", `numberInput[${i}]`);
      numberInput.setAttribute("onchange", `changeValeurOrder(this,${i})`);
      numberInput.setAttribute("value", `${i + 1}`);

      const timeInput = div.querySelector('input[name^="timeInput"]');
      timeInput.setAttribute("id", `timeInput-${i}`);
      timeInput.setAttribute("name", `timeInput[${i}]`);

      const img = div.querySelector("img");
      img.setAttribute("alt", `Image ${i}`);
    });
  } else {
    alert("Image not found!");
  }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Les fonctions ci-dessous pour la page Modifier le service.

// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// La fonction pour supprimer des images existant dans la base de donnée pour un diapo , après l'affichage de la page de modification.
async function deleteOldImage(index) {
  alert(`Êtes-vous sûr de vouloir supprimer l’image à l’indice : ${index}`);
  const deleted = document.getElementById(`DivImageD-${index}`);
  const OrderInput = document.getElementById(`numberInput-${index}`);
  input = remEt(input, OrderInput.value);
  input1 = remEt(input1, OrderInput.value);
  orderIn = remEt(orderIn, orderIn[ orderIn.length - 1]);
  if (deleted) {
    deleted.remove();
    const divImages = document.querySelectorAll(".DivImageD");
    divImages.forEach((div, i) => {
      div.setAttribute("id", `DivImageD-${i}`);
      const deleteButton = div.querySelector("p");
      deleteButton.setAttribute("onclick", `deleteOldImage(${i})`);

      const numberInput = div.querySelector(`input[name^="numberInput"]`);
      numberInput.setAttribute("id", `numberInput-${i}`);
      numberInput.setAttribute("name", `numberInput[${i}]`);
      numberInput.setAttribute("value",`${i + 1}`);
      numberInput.setAttribute("onchange", `changeValeurOrder(this,${i})`);

      const timeInput = div.querySelector(`input[name^="timeInput"]`);
      timeInput.setAttribute("id", `timeInput-${i}`);
      timeInput.setAttribute("name", `timeInput[${i}]`);
      const img = div.querySelector("img");
      img.setAttribute("alt", `Image ${i}`);
    });
  }
  const response = await fetch(
    `/services/deleteImage/${id_service.id}/${index}`
  );
  const data = await response.json();
  const response1 = await fetch(`/services/renameFiles/${id_service.id}`);
  const data1 = await response1.json();
}
//La fonction pour supprimer un élément depuis un tableau.
function remEt(a, ele) {
  const value = parseInt(ele, 10);
  a.forEach((item, index) => {
    if (item === value) {
      a.splice(index, 1);
    }
  });
  return a;
}

// La fonction pour supprimer des nouvelles images ajoutées sur le diapo pour la page de modification avant l'enregistrement.
async function deleteNewImage(index, removed) {
  const confirmation = confirm(
    `Êtes-vous sûr de vouloir supprimer l’image à l’indice : ${index}?`
  );
  if (!confirmation) return;
  const deleted = document.getElementById(`DivImageD-${index}`);
  const orderInput = document.getElementById(`numberInput-${index}`);
  input = remEt(input, orderInput.value);
  input1 = remEt(input1, orderInput.value);
  if (!deleted || !orderInput) {
    alert("Image or input element not found!");
    return;
  }
  const items = dataTransfer.items;
  for (let i = 0; i < items.length; i++) {
    if (i === removed) {
      items.remove(i);
      break;
    }
  }
  const inputTransferData = document.getElementById("inputTransferData");
  if (inputTransferData) {
    inputTransferData.files = dataTransfer.files;
  }
  const fileIndex = parseInt(deleted.getAttribute("data-file-index"), 10);
  deleted.remove();
  const divImages = document.querySelectorAll(".DivImageD");
  divImages.forEach((div, i) => {
    div.setAttribute("id", `DivImageD-${i}`);
    const currentFileIndex = parseInt(div.getAttribute("data-file-index"), 10);
    if (!isNaN(currentFileIndex) && currentFileIndex > fileIndex) {
      div.setAttribute("data-file-index", currentFileIndex - 1);
    }
    const deleteButton = div.querySelector("p");
    deleteButton.setAttribute(
      "onclick",
      `deleteNewImage(${i}, ${parseInt(
        div.getAttribute("data-file-index"),
        10
      )})`
    );
    const numberInput = div.querySelector('input[name^="numberInput"]');
    numberInput.setAttribute("id", `numberInput-${i}`);
    numberInput.setAttribute("name", `numberInput[${i}]`);
    numberInput.setAttribute("onchange", `changeValeurOrder(this,${i})`);
    numberInput.setAttribute("value", `${i + 1}`);
    const timeInput = div.querySelector('input[name^="timeInput"]');
    timeInput.setAttribute("id", `timeInput-${i}`);
    timeInput.setAttribute("name", `timeInput[${i}]`);

    const img = div.querySelector("img");
    img.setAttribute("alt", `Image ${i}`);
  });
}

// Fonction pour ajouter des nouvelles images sur le diapo quand je veux modifier les éléments de service. .
function getFilesModifier(evt) {
  const files = evt.target.files;
  const displayArea = document.getElementById("divFile");
  let divAll = document.getElementById("divAll");
  if (!divAll) {
    divAll = document.createElement("div");
    divAll.setAttribute("id", "divAll");
    document.body.appendChild(divAll);
  }

  const currentCount = divAll.children.length;
  let currentFileIndex = -1;
  if (divAll.lastElementChild) {
    const lastChild = divAll.lastElementChild;
    const dataFileIndex = lastChild.getAttribute("data-file-index");
    currentFileIndex = dataFileIndex ? parseInt(dataFileIndex, 10) : -1;
  }
  if (typeof dataTransfer === "undefined") {
    dataTransfer = new DataTransfer();
  }
  
  Array.from(files).forEach((file, i) => {
    dataTransfer.items.add(file);
    const reader = new FileReader();
    reader.onload = function (e) {
      const index = currentCount + i;
      const DivImg = document.createElement("div");
      DivImg.setAttribute("id", `DivImageD-${index}`);
      DivImg.setAttribute("data-file-index", currentFileIndex + i + 1);
      DivImg.setAttribute("class", "DivImageD");
      DivImg.innerHTML = `
      <p style="width: 100%; text-align: end; cursor: pointer;" 
      class="btn btn-xs" 
      onclick="deleteNewImage(${index}, ${currentFileIndex + i + 1})" 
      data-toggle="tooltip" 
      data-placement="top" 
      title="Supprimer l'image">
      <i class="glyphicon glyphicon-trash" style="color: red;"></i>
      </p>`;
      const img = document.createElement("img");
      img.src = e.target.result;
      img.setAttribute("class", "img1");
      const divInputs = document.createElement("div");
      divInputs.style.display = "flex";
      divInputs.style.flexDirection = "column";
      orderIn.push(index + 1);
      divInputs.innerHTML = `
        <label>Ordre d'affichage:</label>
        <input id="numberInput-${index}" 
               name="numberInput[${index}]" 
               type="number" 
               min="1" 
               class="numberInput"
              value='${index + 1}'
               style="width: 100%; margin-bottom: 10px;" 
               onchange = "changeValeurOrder(this,${index})"}
               >
        <label>Durée d'affichage:</label>
       <div style="position:relative">
              <input id="timeInput-${index}" 
                     name="timeInput[${index}]" 
                     type="number" 
                     class="timeInput"
                     min="1"
                     value='5'
                     style="width: 100%; margin-bottom: 10px;">
                    <span style="position:absolute;right:4px;top:2px">(Seconde)</span>
            </div>`;

      DivImg.appendChild(img);
      DivImg.appendChild(divInputs);
      divAll.appendChild(DivImg);
    };
    reader.readAsDataURL(file);
  });

  const inputTransferData = document.getElementById("inputTransferData");
  if (inputTransferData) inputTransferData.remove();

  const input = document.createElement("input");
  input.type = "file";
  input.name = "service_src1[]";
  input.style.display = "none";
  input.setAttribute("id", "inputTransferData");
  input.files = dataTransfer.files;

  const form = document.querySelector("form");
  form.appendChild(input);
  displayArea.appendChild(divAll);
}

// fonction pour supprimer old fichier pour input file
function clearFileInput(ctrl) {
  try {
    ctrl.value = null;
  } catch (ex) {}
  if (ctrl.value) {
    ctrl.parentNode.replaceChild(ctrl.cloneNode(true), ctrl);
  }
}

function changeStyleErreur(element) {
  element.style.border = "";
}

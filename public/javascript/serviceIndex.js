let positionT = [];
// La fonction pour vérifier la duplication des positions des services.
function validateUpdate(positionPath){
  const selects = document.querySelectorAll("#position_service");
  const positionT = [];
selects.forEach(select => {
  positionT.push(select.value);
});
const duplicates = positionT.filter((item, index) =>{
  return (positionT.indexOf(item) !== index);
});
if (duplicates.length > 0) {
  alert("Les positions des services suivant "+ duplicates +"  sont dupliquées : indiquez des chiffres distincts.");
  return(false)
} else {
  location.href = positionPath;
  return true
}
}
// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//La fonction pour l'extraction de tous les éléments de liste des services. //
function extractionData() {
  const table = document.getElementById("dataTables-example");
  if (!table) {
    console.error('Table with ID "dataTables-example" not found.');
    return [];
  }
  const rows = table.querySelectorAll("tbody tr");
  const extractedData = [];
  rows.forEach((row) => {
    if (
      row.querySelector("td:nth-child(1)")?.textContent.trim() ===
      "Aucun service disponible."
    ) {
      return extractedData;
    }
    const serviceData = {
      logo: row.querySelector("td:nth-child(1) img")?.src || "",
      nom: row.querySelector("td:nth-child(2)")?.textContent.trim() || "",
      position: row.querySelector("td:nth-child(3) select")?.value || "",
      active:
        row.querySelector('td:nth-child(4) input[type="checkbox"]')?.checked ||
        false,
      type: row.querySelector("td:nth-child(5)")?.textContent.trim() || "",
      source: row.querySelector("td:nth-child(6) a")?.textContent.trim() || "",
    };

    extractedData.push(serviceData);
  });
  if (extractedData.length === 0) alert("liste des services est vide ");
  return extractedData;
}
///////////////////////////////////////////////////////////////////////////////////
// l'extraction sous forme EXCEL
function exportCustomExcel() {
  let extractedData = extractionData();
  if (extractedData.length === 0) {
    console.error("No data extracted for export.");
    return;
  }
  const headers = [
    "Icone",
    "Nom du service",
    "Position",
    "Active",
    "Type",
    "Source",
  ];
  const data = extractedData.map((item) => [
    item.logo,
    item.nom,
    item.position,
    item.active ? "Oui" : "Non",
    item.type,
    item.source,
  ]);
  const aoaData = [headers, ...data];
  const ws = XLSX.utils.aoa_to_sheet(aoaData);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "CustomData");
  XLSX.writeFile(wb, "Liste_du_services.xlsx");
}

//  l'extraction sous forme Csv
function extractTableDataCsv() {
  var extractedData = [];
  extractedData = extractionData();
  convertImagesToBase64(extractedData, function (updatedData) {
    const csvData = generateCSV(updatedData);
    downloadCSV(csvData);
  });
}
function generateCSV(data) {
  const csvRows = [];
  const headers = [
    "Icone",
    "Nom de Service",
    "Position",
    "Active",
    "Type",
    "Source",
  ];
  csvRows.push(headers.join(","));
  data.forEach((item) => {
    const row = [
      `"${item.logo}"`,
      `"${item.nom}"`,
      `"${item.position}"`,
      item.active ? "Oui" : "Non",
      `"${item.type}"`,
      `"${item.source}"`,
    ];
    csvRows.push(row.join(","));
  });
  return csvRows.join("\n");
}
function downloadCSV(csvData) {
  const blob = new Blob([csvData], { type: "text/csv" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "Liste du services.csv";
  link.click();
}
///////////////////////////////////////////////////////////////////////////////////

// l'extraction sous forme Pdf
function extractTableData() {
  var extractedData = [];
  extractedData = extractionData();
  convertImagesToBase64(extractedData, function (updatedData) {
    const docDefinition = {
      content: [
        { text: "Liste du services", style: "header" },
        {
          table: {
            body: [
              [
                "Icone",
                "Nom de service",
                "Position",
                "Active",
                "Type",
                "Source",
              ],
              ...updatedData.map((item) => [
                { image: item.logo, width: 40, height: 40 },
                item.nom,
                item.position,
                item.active ? "Oui" : "Non",
                item.type,
                item.source,
              ]),
            ],
          },
        },
      ],
      styles: {
        header: {
          fontSize: 18,
          bold: true,
          alignment: "center",
          margin: [0, 0, 0, 10],
        },
      },
      defaultStyle: {
        fontSize: 12,
        columnWidth: "auto",
      },
    };
    
    pdfMake.createPdf(docDefinition).download("Liste du services.pdf");
  });
}
//  convert les images du liste des services to base64 pour elle l'affiche sur pdf
function convertImagesToBase64(data, callback) {
  let imagesLoaded = 0;
  let totalImages = data.length;
  data.forEach((item) => {
    if (item.logo) {
      const img = new Image();
      img.crossOrigin = "Anonymous";
      img.src = item.logo;

      img.onload = function () {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        canvas.height = img.height;
        canvas.width = img.width;
        ctx.drawImage(img, 0, 0);

        const base64Image = canvas.toDataURL();
        item.logo = base64Image;
        imagesLoaded++;
        if (imagesLoaded === totalImages) {
          callback(data);
        }
      };
    } else {
      imagesLoaded++;
      if (imagesLoaded === totalImages) {
        callback(data);
      }
    }
  });
}

///////////////////////////////////////////////////////////////////////////////////
// .fonction pour ajouter les options sur select position

function get_AllPosition() {
  const selects = document.querySelectorAll("#position_service");
  selects.forEach((select) => {
    const choices = Array.from({ length: 50 }, (_, i) => i + 1);
    const uniqueResult = [...new Set(choices)];
    uniqueResult.forEach((optionData) => {
      const option = document.createElement("option");
      if(select.value == optionData)
      {
        option.setAttribute("selected","selected");
      }
      option.value = optionData;
      option.textContent = optionData;
      select.appendChild(option);
    });
  });
}

///////////////////////////////////////////////////////////////////////////////////
// La fonction retourne les images de diapo de chaque service de type Diapo.
async function getImages(id) {
  try {
    console.log("id: ",id);
    const response = await fetch(`/services/getImage/${id}`);
    console.log(response);
    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
    const data = await response.json();
    console.log("data: ",data);
    return data;
  } catch (error) {
    console.error("Error fetching image:", error);
  }
}


////////////////////////////////////////////////////////////////////////////////////

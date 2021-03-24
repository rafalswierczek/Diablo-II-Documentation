import "../sass/app.scss";

// https://stackoverflow.com/questions/880512/prevent-text-selection-after-double-click
// function clearSelection() {
//     if(document.selection && document.selection.empty) {
//         document.selection.empty();
//     } else if(window.getSelection) {
//         var sel = window.getSelection();
//         sel.removeAllRanges();
//     }
// }

// document.querySelector("#account-icon-label > span").addEventListener("click", ()=>{
//     clearSelection();
// });


let downloadLiList = document.querySelectorAll('#downloadList > li');
if(downloadLiList.length)
{
    [...downloadLiList].forEach(elem => {
        let link = elem.querySelector('a').href;
        elem.addEventListener('click', ()=>{
            location.href = link;
        });
    });
}

let documentationCreateGroupList = document.querySelectorAll('#documentation-create-form .form-group');
if(documentationCreateGroupList.length)
{
    [...documentationCreateGroupList].forEach(group => {
        let fileInput = group.querySelector('input[type="file"]');
        if(fileInput)
        {
            fileInput.addEventListener('input', ()=>{
                let label = group.querySelector('label');
                let inputEmpty = fileInput.files.length === 0 ? true : false;
                if(inputEmpty)
                    label.style.background = "#333";
                else
                    label.style.background = "#2C632B";
            });
        }
    });
}


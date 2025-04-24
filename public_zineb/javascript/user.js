function validateUtilisateur(){
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    const username = document.getElementById('contact_Identifiant');
    const email = document.getElementById('contact_email');
    const password =  document.getElementById('contact_plainPassword_first');
    const confirmPassword = document.getElementById('contact_plainPassword_second');
    if(username.value.trim() == '')
    { 
        username.style.border ='2px solid red';
        const ErreurNom = document.getElementById('ErreurNom');
        if(!ErreurNom)
        {
        const p = document.createElement('p');
        p.setAttribute('id','ErreurNom');
        p.textContent = 'Le nom d\'utilisateur est obligatoire ';
        p.style.color ='red';
        document.getElementById('requiredNom').appendChild(p);
        }
        return false;
    }
    if(email.value.trim() == '')
    { 
        email.style.border ='2px solid red';
        const ErreurEmail = document.getElementById('ErreurEmail');
        if(!ErreurEmail)
        {
        const p = document.createElement('p');
        p.setAttribute('id','ErreurEmail');
        p.textContent = 'L \'email d\'utilisateur est obligatoire ';
        p.style.color ='red';
        document.getElementById('requiredEmail').appendChild(p);
        }
        return false;
    }
    if (!email.value.match(validRegex)) {
        email.style.border ='2px solid red';
        const ErreurEmail = document.getElementById('ErreurEmailValid');
        if(!ErreurEmail)
        {
            const p = document.createElement('p');
            p.setAttribute('id','ErreurEmailValid');
            p.textContent = 'Entrez un email valide s\'il vous plait ';
            p.style.color ='red';
            document.getElementById('requiredEmail').appendChild(p);
        }
        return false;
    }
    // if(password.value.trim() == '')
    // { 
    //     password.style.border ='2px solid red';
    //     const ErreurPassword = document.getElementById('ErreurPassword');
    //     if(!ErreurPassword)
    //     {
    //     const p = document.createElement('p');
    //     p.setAttribute('id','ErreurPassword');
    //     p.textContent = 'Le mot de passe d\'utilisateur est obligatoire ';
    //     p.style.color ='red';
    //     document.getElementById('requiredFirstPassword').appendChild(p);
    //     }
    //     return false;
    // }
    if(confirmPassword.value.trim() == '' && password.value.trim() != '')
    { 
        confirmPassword.style.border ='2px solid red';
        const ErreurConfirmePassword = document.getElementById('ErreurConfirmePassword');
        if(!ErreurConfirmePassword)
        {
        const p = document.createElement('p');
        p.setAttribute('id','ErreurConfirmePassword');
        p.textContent = 'Confirmer le mot de passe s\'il vous plait';
        p.style.color ='red';
        document.getElementById('requiredSecondPassword').appendChild(p);
        }
        return false;
    }
    if(password.value !== confirmPassword.value  && password.value.trim() != '')
    {
        const ErreurConfirmePassword = document.getElementById('ErreuregalPassword');
        if(!ErreurConfirmePassword)
        {
        const p = document.createElement('p');
        p.setAttribute('id','ErreuregalPassword');
        p.textContent = 'Entrez le même mot de passe s\'il vous plait';
        p.style.color ='red';
        document.getElementById('requiredSecondPassword').appendChild(p);
        }
        return false
    }
}
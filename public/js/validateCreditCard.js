//------------------------------------------
// Fichier: validateCreditCard.js
// Rôle: Contrôleur des page de gestion de commandes
// Création: 2021-04-22
// Par: Kevin St-Pierre
//--------------------------------------------

const ERROR_TYPE = 'Vous devez sélectionner le type de carte.';
const ERROR_NUMBER_TYPE = 'Le format du numéro de carte ne correspond pas au type.';
const ERROR_INVALID_NUMBER = 'Le numéro de carte est invalide.';
const ERROR_DATE = 'La date d\'expiration est dépassée.';


//----------------------------------------
// Valide la carte de crédit
//----------------------------------------
function validateCreditCard(form) {
    type = $("input[name='creditCard']:checked").val();
    cardNumber = form[3].value;
    month = parseInt(form[4].value);
    year = parseInt(form[5].value);

    if (type == null) {
        alert(ERROR_TYPE);
        return false;
    }

    if (!validateType(type, cardNumber)) {
        alert(ERROR_NUMBER_TYPE);
        return false;
    }

    if (!validateModulo(cardNumber)) {
        alert(ERROR_INVALID_NUMBER);
        return false;
    }

    if (!validateDate(month, year)) {
        alert(ERROR_DATE);
        return false;
    }

    return true;
}

//----------------------------------------
// Valide que le numéro de carte correspond
// au modèle du fourniesseur
//----------------------------------------
function validateType(type, cardNumber) {

    switch (type) {
        case 'V':
            ptnVisa = /^4\d{12}(\d{3})?$/;
            //#region Test
            // Numéro valide 4242424242424
            // Numéro valide 4242424242424242
            // Numéro invalide 5242424242424
            // Numéro invalide 5242424242424242
            // Numéro invalide 424242424242
            // Numéro invalide 42424242424242
            // Numéro invalide 42424242424242424
            //#endregion
            if (!ptnVisa.test(cardNumber)) return false;
            break;

        case 'M':
            ptnMasterCard = /^5[1-5]\d{14}$/;
            //#region Test
            // Numéro valide 5142424242424242
            // Numéro valide 5242424242424242
            // Numéro valide 5342424242424242
            // Numéro valide 5442424242424242
            // Numéro valide 5542424242424242
            // Numéro invalide 6542424242424242
            // Numéro invalide 5642424242424242
            // Numéro invalide 554242424242424
            // Numéro invalide 55424242424242424
            //#endregion
            if (!ptnMasterCard.test(cardNumber)) return false;
            break;

        case 'A':
            ptnAmericanExpress = /^3[47]\d{13}$/;
            //#region Test
            // Numéro valide 344242424242424
            // Numéro valide 374242424242424
            // Numéro invalide 444242424242424
            // Numéro invalide 334242424242424
            // Numéro invalide 34424242424242
            // Numéro invalide 3442424242424242
            //#endregion
            if (!ptnAmericanExpress.test(cardNumber)) return false;
            break;
    }
    return true;
}

//----------------------------------------
// Valide que le numéro de carte à l'Algo-Modulo10
//----------------------------------------
function validateModulo(cardNumber) {
    // Numéro valide 4510156018359542
    numbers = [];
    for (let i = cardNumber.length - 1; i >= 0; i--)
        numbers.push(parseInt(cardNumber[i], 10));

    var sumEvenIndex = 0;
    var sumOddIndex = 0;
    for (let i = 0; i < numbers.length; i++)
        if (i % 2 == 0)
            sumEvenIndex += numbers[i];
        else {
            var number = numbers[i] * 2;
            if (number > 10)
                number = 1 + number - 10;
            sumOddIndex += number;
        }

    if ((sumEvenIndex + sumOddIndex) % 10 != 0)
        return false;
    return true;
}

//----------------------------------------
// Valide que la carte n'est pas expirée
//----------------------------------------
function validateDate(month, year) {
    var today = new Date();
    currentMonth = today.getMonth() + 1;
    currentYear = today.getFullYear();
    if (year < currentYear ||
        (year == currentYear && month < currentMonth)) {
        return false;
    }
    return true;
}
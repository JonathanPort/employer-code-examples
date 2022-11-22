import Module from './Module';

export default class PasswordStrengthMetres extends Module {

    constructor() {

        super();

        this.moduleName = 'PasswordStrengthMetres';

    }

    start() {

        const metres = document.querySelectorAll('[data-password-strength-metre]');

        for (let i = 0; i < metres.length; i++) {
            PasswordStrengthMetre(metres[i]);
        }

        if (metres.length) return super.logLoaded();

    }

}




const PasswordStrengthMetre = (elem) => {

    const formName = elem.getAttribute('data-password-strength-metre');
    const bar = elem.querySelector('[data-password-strength-metre-bar]');
    const text = elem.querySelector('[data-password-strength-metre-text]');
    const input = document.querySelector('[data-password-strength-input="' + formName + '"]');
    const hiddenInput = document.querySelector('[data-password-strength-hidden-input="' + formName + '"]');
    const oneLowercaseReg = '(?=.*[a-z])'; // score 1
    const oneUppercaseReg = '(?=.*[A-Z])'; // score 1
    const oneDigitReg = '(?=.*[0-9])'; // score 1
    const oneSpecialCharReg = '([^A-Za-z0-9])'; // score 1
    const twelveCharsLongReg = '(?=.{12,})'; // score 2
    const sixteenCharsLongReg = '(?=.{16,})'; // score 3

    const levels = [
        {
            label: 'Contains at least 1 lowercase character',
            reg: oneLowercaseReg,
            score: 1,
        },
        {
            label: 'Contains at least 1 uppercase character',
            reg: oneUppercaseReg,
            score: 1,
        },
        {
            label: 'Contains at least 1 number',
            reg: oneDigitReg,
            score: 1,
        },
        {
            label: 'Contains at least 1 special character',
            reg: oneSpecialCharReg,
            score: 1,
        },
        {
            label: 'Password is at least 12 characters long',
            reg: twelveCharsLongReg,
            score: 1,
        },
        {
            label: 'Password is at least 16 characters long',
            reg: sixteenCharsLongReg,
            score: 1,
        },
    ];

    const strengthFunc = () => {

        let score = 0;
        let textValue = false;
        let barValue = false;

        let value = input.value;

        if (! value.length) {

            elem.classList.remove('visible');
            text.innerHTML = '';

        } else {

            let reg = false;
            let maxScore = 0;

            elem.classList.add('visible');

            for (let i in levels) {

                reg = new RegExp(levels[i].reg);

                if (reg.test(value)) {
                    score += levels[i].score;
                }

                maxScore += levels[i].score;

            }

            if (score === 1) {
                textValue = 'insecure';
            } else if (score < 3) {
                textValue = 'weak';
            } else if (score < 5) {
                textValue = 'fair';
            } else if (score < maxScore) {
                textValue = 'okay';
            } else {
                textValue = 'secure';
            }

            text.innerHTML = textValue;

            let percentage = (score / maxScore) * 100;

            bar.style.width = percentage + '%';

            bar.classList.remove('insecure');
            bar.classList.remove('weak');
            bar.classList.remove('fair');
            bar.classList.remove('okay');
            bar.classList.remove('secure');

            bar.classList.add(textValue);

            hiddenInput.value = textValue;

        }

    };

    input.addEventListener('keyup', strengthFunc);
    input.addEventListener('onpaste', strengthFunc);

};

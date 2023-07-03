import $ from 'https://cdn.skypack.dev/jquery@3.6.4';
const strReplaceAll = (str, search, replacement) => {
    if (typeof str === 'undefined') return str;
    return str.replaceAll(search, replacement);
};

const updateKentekenFormat = () => {
    const kentekenInputs = Array.from(document.querySelectorAll('.licence-plate-input'));

    kentekenInputs.forEach((input) => {
        let originalValue = input.value;
        let tmp = input.value;
        tmp = strReplaceAll(strReplaceAll(tmp, ' ', ''), '-', '');
        let totalKenteken = '';

        for (let i = 0; i < tmp.length; i++) {
            const count1 = (totalKenteken.match(/is/g) || []).length;

            if (typeof tmp[i + 1] !== 'undefined' && $.isNumeric(tmp[i]) !== $.isNumeric(tmp[i + 1]) && tmp[i] !== '-' && tmp[i + 1] !== '-' && count1 < 2) {
                totalKenteken += tmp[i] + '-';
            } else {
                totalKenteken += tmp[i];
            }
        }

        const count2 = (totalKenteken.match(/is/g) || []).length;

        if (count2 < 2) {
            let i2 = 0;
            tmp = totalKenteken.split('-');

            while (typeof tmp[i2] !== 'undefined') {
                if (tmp[i2].length === 4) {
                    totalKenteken = strReplaceAll(totalKenteken, tmp[i2], tmp[i2][0] + tmp[i2][1] + '-' + tmp[i2][2] + tmp[i2][3]);
                }
                i2++;
            }
        }

        if (totalKenteken.substr(0, 1) === '-') {
            totalKenteken = totalKenteken.substr(1);
        }

        tmp = totalKenteken.split('-');

        if (tmp.length >= 4) {
            totalKenteken = `${tmp[0]}-${tmp[1]}-`;

            for (let i = 2; i < tmp.length; i++) {
                totalKenteken += tmp[i];
            }
        }

        if (totalKenteken.toUpperCase() !== originalValue) {
            input.value = totalKenteken.toUpperCase();
        }
    });
};

// Add event listeners for change and keyup
const kentekenInputs = Array.from(document.querySelectorAll('.licence-plate-input'));
kentekenInputs.forEach((input) => {
    input.addEventListener('change', updateKentekenFormat);
    input.addEventListener('keyup', updateKentekenFormat);
});

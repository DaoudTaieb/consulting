const fs = require('fs');
const path = require('path');

const dir = path.join(__dirname, 'resources', 'js', 'Pages');
const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));

let count = 0;
const regex = /const formatCurrency = \(val\) => \{\s*return new Intl\.NumberFormat\('fr-TN', \{ maximumFractionDigits: 3 \}\)\.format\(Number\(val\)\);\s*\}/g;

const replacement = `const formatCurrency = (val) => {
    return Number(val || 0).toFixed(3).replace(/\\B(?=(\\d{3})+(?!\\d))/g, ' ');
}`;

for (const file of files) {
    const fullPath = path.join(dir, file);
    let content = fs.readFileSync(fullPath, 'utf-8');
    
    if (regex.test(content)) {
        content = content.replace(regex, replacement);
        fs.writeFileSync(fullPath, content);
        console.log(`Replaced in ${file}`);
        count++;
    }
}

console.log(`Total files updated: ${count}`);

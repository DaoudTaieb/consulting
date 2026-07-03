const fs = require('fs');
const path = require('path');

const dir = path.join(__dirname, 'resources', 'js', 'Pages');
const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));

let count = 0;

for (const file of files) {
    const fullPath = path.join(dir, file);
    let content = fs.readFileSync(fullPath, 'utf-8');
    
    const regex = /const formatCurrency = \(val\) => \{[\s\S]*?\};/g;
    
    const replacement = `const formatCurrency = (val) => {
    if (val == null) return '0.000';
    return Number(val).toFixed(3).replace(/\\B(?=(\\d{3})+(?!\\d))/g, ' ');
};`;

    if (regex.test(content)) {
        content = content.replace(regex, replacement);
        fs.writeFileSync(fullPath, content);
        console.log(`Replaced in ${file}`);
        count++;
    }
}

console.log(`Total files updated: ${count}`);

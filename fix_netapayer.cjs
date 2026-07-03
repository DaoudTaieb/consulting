const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'routes', 'web.php');
let content = fs.readFileSync(file, 'utf-8');

// replace all instances of netapayer with totalttc
content = content.replace(/netapayer/g, 'totalttc');

fs.writeFileSync(file, content);
console.log('Replaced netapayer with totalttc in web.php');

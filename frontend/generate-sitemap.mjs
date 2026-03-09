import fs from 'fs';
import path from 'path';

const api_key = '/';
const endpoint_url = `https://exemplo.dev.br/endpoints/endpoint.php?action=get_all_data&api_key=${api_key}`;

const BASE_URL = 'https://exemplo.dev.br';

async function generateSitemap() {
    console.log('Fetching data from API for sitemap...');
    const response = await fetch(endpoint_url);
    const result = await response.json();

    const edicoes = result.data?.edicoes || [];

    const staticRoutes = [
        '/',
        '/sobre',
        '/expediente',
        '/edicoes',
        '/galeria'
    ];

    const today = new Date().toISOString().split('T')[0];

    let xml = `<?xml version="1.0" encoding="UTF-8"?>\n`;
    xml += `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n`;

    for (const route of staticRoutes) {
        xml += `  <url>\n`;
        xml += `    <loc>${BASE_URL}${route}</loc>\n`;
        xml += `    <lastmod>${today}</lastmod>\n`;
        xml += `    <changefreq>${route === '/' ? 'weekly' : 'monthly'}</changefreq>\n`;
        xml += `    <priority>${route === '/' ? '1.0' : '0.8'}</priority>\n`;
        xml += `  </url>\n`;
    }

    for (const ed of edicoes) {
        const num = ed.numero_edicao.replace('Edição #', '').trim();
        xml += `  <url>\n`;
        xml += `    <loc>${BASE_URL}/edicoes/edicao/${num}</loc>\n`;
        xml += `    <lastmod>${today}</lastmod>\n`;
        xml += `    <changefreq>monthly</changefreq>\n`;
        xml += `    <priority>0.9</priority>\n`;
        xml += `  </url>\n`;
    }

    xml += `</urlset>\n`;

    const outputPath = path.join(process.cwd(), 'public', 'sitemap.xml');
    fs.writeFileSync(outputPath, xml, 'utf8');

    console.log(`Sitemap successfully generated at ${outputPath} with ${staticRoutes.length + edicoes.length} URLs.`);
}

generateSitemap().catch(console.error);

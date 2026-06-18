import fs from "fs";

const SHOPIFY_CDN =
  "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/";

const collections = [
  "new-arrivals",
  "led-tvs",
  "sound-bars",
  "air-purifiers",
  "home-theater",
  "blu-ray-movies",
  "4k-moives",
  "lp-records",
  "lp-record",
  "vinyl-records",
  "audio",
  "audio-equipment",
  "mobile-accessories",
  "tv-trolly-stand",
  "accessories",
  "posters",
  "dvd",
  "dvd-movies",
  "books",
  "books-media",
  "cameras",
  "computing",
  "smart-gadgets",
  "home-appliances",
  "kitchen-appliances",
  "cartridges",
  "speakers",
  "headphones",
];

const pages = ["/", ...collections.map((c) => `/collections/${c}`)];

function decodeImageUrl(encoded) {
  try {
    return decodeURIComponent(encoded);
  } catch {
    return encoded;
  }
}

function parseProductsFromHtml(html, category) {
  const products = [];
  const seen = new Set();

  const patterns = [
    /href="(\/product\/[^"]+)"[\s\S]*?alt="([^"]+)"[\s\S]*?srcSet="\/_next\/image\?url=([^&"]+)[\s\S]*?Rs\s*([\d,]+)/g,
    /alt="([^"]+)"[\s\S]*?href="(\/product\/[^"]+)"[\s\S]*?srcSet="\/_next\/image\?url=([^&"]+)[\s\S]*?Rs\s*([\d,]+)/g,
  ];

  for (const pattern of patterns) {
    let match;
    while ((match = pattern.exec(html)) !== null) {
      const slug = (match[1].startsWith("/product/") ? match[1] : match[2])
        .replace("/product/", "");
      const name = match[1].startsWith("/product/") ? match[2] : match[1];
      const image = decodeImageUrl(match[3]);
      const price = Number(match[4].replace(/,/g, ""));
      if (!seen.has(slug) && name && !Number.isNaN(price)) {
        seen.add(slug);
        products.push({
          id: slug,
          name,
          slug,
          price,
          image,
          imageAlt: name,
          category,
        });
      }
    }
  }

  return products;
}

async function scrapePage(path, category) {
  const response = await fetch(`https://mybeststore.pk${path}`);
  const html = await response.text();
  return parseProductsFromHtml(html, category);
}

async function main() {
  const byCategory = {};
  const allProducts = [];

  for (const path of pages) {
    const category =
      path === "/" ? "homepage" : path.replace("/collections/", "");
    try {
      const products = await scrapePage(path, category);
      byCategory[category] = products;
      allProducts.push(...products);
      if (products.length > 0) {
        console.log(`${category}: ${products.length}`);
      }
    } catch (error) {
      console.log(`${category}: ERROR ${error.message}`);
      byCategory[category] = [];
    }
  }

  const unique = new Map();
  for (const product of allProducts) {
    if (!unique.has(product.slug)) unique.set(product.slug, product);
  }

  fs.writeFileSync(
    "scripts/scraped-products.json",
    JSON.stringify({ byCategory, all: [...unique.values()] }, null, 2)
  );

  console.log(`\nTotal unique products: ${unique.size}`);
  console.log(`Categories with products: ${Object.values(byCategory).filter((x) => x.length).length}`);
}

main();

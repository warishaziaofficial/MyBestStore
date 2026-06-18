import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.join(__dirname, "..");

const ASSETS = [
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/71_Brvs_lXS._AC_SL1500.jpg?v=1681636742&width=1200",
    dest: "public/hero/hero-main.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg?v=1681636528&width=1200",
    dest: "public/hero/hero-tv.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/912ndJ23FgL._AC_SL1500.jpg?v=1681636750&width=1200",
    dest: "public/hero/hero-theater.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg?v=1681636528&width=600",
    dest: "public/categories/led-tvs.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/Alpha-VS21-BLK-1000x1000.jpg?v=1681636520&width=600",
    dest: "public/categories/sound-bars.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/shasha2625_101582868359.jpg?v=1681636520&width=600",
    dest: "public/categories/air-purifiers.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/pioneer-htz272-code-free-home-theatre-system-for-110-240-volts-20.jpg?v=1681636520&width=600",
    dest: "public/categories/home-theater.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/712HtUkI_TL._AC_SX569.jpg?v=1681636117&width=600",
    dest: "public/categories/audio-equipment.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/81JLnCHVlfL._AC_SX425.jpg?v=1681636520&width=600",
    dest: "public/categories/blu-ray-movies.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/41_W4m85vTL.jpg?v=1681636520&width=600",
    dest: "public/categories/4k-movies.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/dilwale-svr-005-cover-book-fold-red-coloured-lp-record-coming-date-till-20th-to-25th-july-1.jpg?v=1681636138&width=600",
    dest: "public/categories/lp-records.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/BT185H-pix.jpg?v=1681636520&width=600",
    dest: "public/categories/mobile-accessories.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/26-32inch-TV-Ceiling-Roof-Mount-Bracket-LCD-LED-Monitor-Holder-Flat-Tilting-Tools-Accessory-Loading.png?v=1681636520&width=600",
    dest: "public/categories/tv-stands.png",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/High-Quality-Wireless-HDMI-Extender-50M-HD-1080P-Video-Audio-Signal-Transmission-System-HDMI-wireless-Extender.jpg?v=1681636520&width=600",
    dest: "public/categories/accessories.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/PanasonicF-PXJ30ANon-HumidifyingNanoeAirPurifier_White.jpg?v=1718359220&width=800",
    dest: "public/banners/appliances.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/pioneer-htz272-code-free-home-theatre-system-for-110-240-volts-20.jpg?v=1681636520&width=800",
    dest: "public/banners/home-entertainment.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1593784991095-a205069470b6?w=800&auto=format&fit=crop&q=80",
    dest: "public/blog/qled-tv-guide.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80",
    dest: "public/blog/home-audio.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&auto=format&fit=crop&q=80",
    dest: "public/blog/new-arrivals.jpg",
  },
];

async function download(url, dest) {
  const filePath = path.join(root, dest);
  fs.mkdirSync(path.dirname(filePath), { recursive: true });

  const response = await fetch(url, {
    headers: { "User-Agent": "MyBestStore-Image-Sync/1.0" },
  });

  if (!response.ok) {
    throw new Error(`${response.status} ${response.statusText}`);
  }

  const buffer = Buffer.from(await response.arrayBuffer());
  fs.writeFileSync(filePath, buffer);
  console.log(`✓ ${dest}`);
}

let failed = 0;
for (const asset of ASSETS) {
  try {
    await download(asset.url, asset.dest);
  } catch (error) {
    failed += 1;
    console.error(`✗ ${asset.dest}: ${error.message}`);
  }
}

console.log(`Done. ${ASSETS.length - failed}/${ASSETS.length} images saved.`);

import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.join(__dirname, "..");

/** @type {{ url: string; file: string }[]} */
const assets = [
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/71_Brvs_lXS._AC_SL1500.jpg?v=1681636742&width=1600",
    file: "public/hero/hero-main.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg?v=1681636528&width=1600",
    file: "public/hero/hero-tv.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/912ndJ23FgL._AC_SL1500.jpg?v=1681636750&width=1600",
    file: "public/hero/hero-theater.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg?v=1681636528&width=700",
    file: "public/categories/led-tvs.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/Alpha-VS21-BLK-1000x1000.jpg?v=1681636520&width=700",
    file: "public/categories/sound-bars.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/shasha2625_101582868359.jpg?v=1681636520&width=700",
    file: "public/categories/air-purifiers.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/pioneer-htz272-code-free-home-theatre-system-for-110-240-volts-20.jpg?v=1681636520&width=700",
    file: "public/categories/home-theater.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/712HtUkI_TL._AC_SX569.jpg?v=1681636117&width=700",
    file: "public/categories/audio-equipment.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/81JLnCHVlfL._AC_SX425.jpg?v=1681636520&width=700",
    file: "public/categories/blu-ray-movies.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/41_W4m85vTL.jpg?v=1681636520&width=700",
    file: "public/categories/4k-moives.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/dilwale-svr-005-cover-book-fold-red-coloured-lp-record-coming-date-till-20th-to-25th-july-1.jpg?v=1681636138&width=700",
    file: "public/categories/lp-records.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/BT185H-pix.jpg?v=1681636520&width=700",
    file: "public/categories/mobile-accessories.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/26-32inch-TV-Ceiling-Roof-Mount-Bracket-LCD-LED-Monitor-Holder-Flat-Tilting-Tools-Accessory-Loading.png?v=1681636520&width=700",
    file: "public/categories/tv-trolly-stand.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/High-Quality-Wireless-HDMI-Extender-50M-HD-1080P-Video-Audio-Signal-Transmission-System-HDMI-wireless-Extender.jpg?v=1681636520&width=700",
    file: "public/categories/accessories.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/51oKFzORCHL._AC_SL1500.jpg?v=1681636528&width=1200",
    file: "public/banners/electronics-deals.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/Alpha-VS21-BLK-1000x1000.jpg?v=1681636520&width=1200",
    file: "public/banners/audio-collection.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/PanasonicF-PXJ30ANon-HumidifyingNanoeAirPurifier_White.jpg?v=1718359220&width=1200",
    file: "public/banners/appliances-offers.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/912ndJ23FgL._AC_SL1500.jpg?v=1681636750&width=1200",
    file: "public/banners/featured-deal.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/pioneer-htz272-code-free-home-theatre-system-for-110-240-volts-20.jpg?v=1681636520&width=1200",
    file: "public/banners/home-entertainment.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/KG-G40E-W-2.png?v=1681636520&width=1200",
    file: "public/banners/smart-home.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/Pioneer_HTZ787DVD_HTZ787DVD_All_Region_DVD_673526.jpg?v=1681637189&width=1200",
    file: "public/banners/audio-entertainment.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1593784991095-a205069470b6?w=800&auto=format&fit=crop&q=80",
    file: "public/blog/qled-tv-guide.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80",
    file: "public/blog/home-audio.jpg",
  },
  {
    url: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&auto=format&fit=crop&q=80",
    file: "public/blog/new-arrivals.jpg",
  },
  {
    url: "https://cdn.shopify.com/s/files/1/0746/4730/6554/products/71_Brvs_lXS._AC_SL1500.jpg?v=1681636742&width=600",
    file: "public/products/showcase-soundbar.jpg",
  },
];

async function download(asset) {
  const dest = path.join(root, asset.file);
  if (fs.existsSync(dest) && fs.statSync(dest).size > 0) {
    console.log("skip", asset.file);
    return true;
  }

  fs.mkdirSync(path.dirname(dest), { recursive: true });

  try {
    const res = await fetch(asset.url, {
      headers: { "User-Agent": "MyBestStore-ImageCache/1.0" },
    });
    if (!res.ok) {
      console.warn("fail", asset.file, res.status);
      return false;
    }
    const buf = Buffer.from(await res.arrayBuffer());
    if (buf.length < 500) {
      console.warn("tiny", asset.file);
      return false;
    }
    fs.writeFileSync(dest, buf);
    console.log("saved", asset.file, buf.length);
    return true;
  } catch (error) {
    console.warn("error", asset.file, error.message);
    return false;
  }
}

let ok = 0;
for (const asset of assets) {
  if (await download(asset)) ok += 1;
}
console.log(`Done: ${ok}/${assets.length} images cached.`);

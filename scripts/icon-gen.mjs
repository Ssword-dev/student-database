import favicons from "favicons";
import fs from "fs";
import { promisify } from "util";

const faviconsAsync = promisify(favicons);

async function generateIcons() {
  const source = "src/assets/icon.png";
  const configuration = {
    path: "/",
    appName: "My App",
    icons: {
      android: true,
      appleIcon: true,
      favicons: true,
    },
  };

  try {
    const response = await faviconsAsync(source, configuration);

    response.images.forEach((img) =>
      fs.writeFileSync(`./icons/${img.name}`, img.contents),
    );
    response.files.forEach((file) =>
      fs.writeFileSync(`./icons/${file.name}`, file.contents),
    );

    console.log("Icons generated successfully!");
  } catch (err) {
    console.error(err);
  }
}

generateIcons();

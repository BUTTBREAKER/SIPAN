import { sassPlugin } from "esbuild-sass-plugin";

/** @type {import('esbuild').BuildOptions} */
export const commonOptions = {
  bundle: true,
  entryPoints: ["assets/src/layouts/*.ts"],
  format: "esm",
  loader: {
    ".module.css": "local-css",
    ".ttf": "copy",
    ".woff": "copy",
    ".woff2": "copy",
    ".jpg": "copy",
    ".webp": "copy",
    ".png": "copy",
    ".svg": "dataurl",
  },
  outdir: "assets/dist",
  target: ["es2018"],
  plugins: [
    sassPlugin({
      silenceDeprecations: [
        "import",
        "global-builtin",
        "function-units",
        "color-functions",
        "mixed-decls",
      ],
    }),
  ],
};

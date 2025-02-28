import { build } from "esbuild";
import { commonOptions } from "./config.mjs";

await build({
  ...commonOptions,
  minify: true,
});

console.info("Compiled successfully");

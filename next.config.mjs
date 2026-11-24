import createMDX from "@next/mdx";

// Articles live in content/*.mdx. Plugins are passed by name so Turbopack can load them.
const withMDX = createMDX({
  options: {
    remarkPlugins: [["remark-gfm"]],
    rehypePlugins: [["rehype-slug"]],
  },
});

export default withMDX({ pageExtensions: ["ts", "tsx", "mdx"] });

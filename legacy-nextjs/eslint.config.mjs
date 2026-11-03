import nextConfig from "eslint-config-next";

const eslintConfig = [
  ...nextConfig,
  {
    ignores: ["legacy-php/**", "node_modules/**", ".next/**"],
  },
];

export default eslintConfig;

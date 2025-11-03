module.exports = {
    plugins: [
        require("postcss-import"),
        require("@tailwindcss/postcss")("./tailwind.config.js"),
        require("autoprefixer"),
    ],
};

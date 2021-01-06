# Cartback - Abandoned cart Mailchimp automation for WooCommerce

## Development

For the Javascript side of things, the source is located in the `./js` folder.
This is the source code and will be build with webpack + babel and compiled to `./public`
The main output file is `./public/cartback.min.js`

Compilation happens automatically with a pre-push hook, so when you push a new build gets created.

### Getting Started

```shell
npm install
npm run build
```

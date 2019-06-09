class StyleImporter {
  constructor() {
  }

  backendLoaded = () => {
    const importRenderElement = document.querySelector('#js-stylepress-import-wizard');
    if(importRenderElement) {
      const App = () => (
        <div>
          Test123
        </div>
      );
      wp.element.render(<App/>, importRenderElement);
    }

  };
}

export let style_importer = new StyleImporter();

import { Route } from "react-router-dom"
import { config } from "../../util/config";

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

  backendRoute = () => {
    return <Route
      path="/embed"
      exact
      render={(props) => {
        return <div>test embed</div>
      }}
    />
  }
}

export let style_importer = new StyleImporter();

import React from 'react';
import ReactDOM from 'react-dom';
import APMainMenuLogo from './APMainMenuLogo';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMainMenuLogo />, div);
  ReactDOM.unmountComponentAtNode(div);
});
import React from 'react';
import ReactDOM from 'react-dom';
import APMainMenu from './APMainMenu';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMainMenu />, div);
  ReactDOM.unmountComponentAtNode(div);
});
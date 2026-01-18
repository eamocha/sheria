import React from 'react';
import ReactDOM from 'react-dom';
import APGlobalLoader from './APGlobalLoader';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APGlobalLoader />, div);
  ReactDOM.unmountComponentAtNode(div);
});
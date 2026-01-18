import React from 'react';
import ReactDOM from 'react-dom';
import APGlobalWalkThrough from './APGlobalWalkThrough';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APGlobalWalkThrough />, div);
  ReactDOM.unmountComponentAtNode(div);
});
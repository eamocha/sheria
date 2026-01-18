import React from 'react';
import ReactDOM from 'react-dom';
import APMainMenuItem from './APMainMenuItem';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMainMenuItem />, div);
  ReactDOM.unmountComponentAtNode(div);
});
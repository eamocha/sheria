import React from 'react';
import ReactDOM from 'react-dom';
import APPageBody from './APPageBody';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APPageBody />, div);
  ReactDOM.unmountComponentAtNode(div);
});
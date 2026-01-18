import React from 'react';
import ReactDOM from 'react-dom';
import People from './People';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<People />, div);
  ReactDOM.unmountComponentAtNode(div);
});
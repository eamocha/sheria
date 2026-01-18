import React from 'react';
import ReactDOM from 'react-dom';
import Contributors from './Contributors';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Contributors />, div);
  ReactDOM.unmountComponentAtNode(div);
});
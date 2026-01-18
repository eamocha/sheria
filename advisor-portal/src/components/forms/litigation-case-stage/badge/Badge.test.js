import React from 'react';
import ReactDOM from 'react-dom';
import Badge from './Badge';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Badge />, div);
  ReactDOM.unmountComponentAtNode(div);
});
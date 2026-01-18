import React from 'react';
import ReactDOM from 'react-dom';
import Dates from './Dates';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<Dates />, div);
  ReactDOM.unmountComponentAtNode(div);
});
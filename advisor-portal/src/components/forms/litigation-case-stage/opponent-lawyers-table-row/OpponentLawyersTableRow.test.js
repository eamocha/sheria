import React from 'react';
import ReactDOM from 'react-dom';
import OpponentLawyersTableRow from './OpponentLawyersTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentLawyersTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
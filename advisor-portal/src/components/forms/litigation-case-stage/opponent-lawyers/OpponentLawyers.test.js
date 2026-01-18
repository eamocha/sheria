import React from 'react';
import ReactDOM from 'react-dom';
import OpponentLawyers from './OpponentLawyers';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentLawyers />, div);
  ReactDOM.unmountComponentAtNode(div);
});
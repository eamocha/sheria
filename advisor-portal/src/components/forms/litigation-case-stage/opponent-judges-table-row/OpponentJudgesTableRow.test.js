import React from 'react';
import ReactDOM from 'react-dom';
import OpponentJudgesTableRow from './OpponentJudgesTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<OpponentJudgesTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
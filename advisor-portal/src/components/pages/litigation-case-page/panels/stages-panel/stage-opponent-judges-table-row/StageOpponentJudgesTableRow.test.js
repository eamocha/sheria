import React from 'react';
import ReactDOM from 'react-dom';
import StageOpponentJudgesTableRow from './StageOpponentJudgesTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageOpponentJudgesTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
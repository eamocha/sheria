import React from 'react';
import ReactDOM from 'react-dom';
import StageOpponentJudgesTable from './StageOpponentJudgesTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageOpponentJudgesTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});
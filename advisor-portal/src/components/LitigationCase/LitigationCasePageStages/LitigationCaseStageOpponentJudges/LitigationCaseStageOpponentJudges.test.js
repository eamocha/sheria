import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageOpponentJudges from './LitigationCaseStageOpponentJudges';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageOpponentJudges />, div);
  ReactDOM.unmountComponentAtNode(div);
});
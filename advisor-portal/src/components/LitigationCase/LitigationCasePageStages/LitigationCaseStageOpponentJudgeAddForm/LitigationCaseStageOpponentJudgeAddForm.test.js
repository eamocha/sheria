import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageOpponentJudgeAddForm from './LitigationCaseStageOpponentJudgeAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageOpponentJudgeAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});
import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageOpponentLawyerAddForm from './LitigationCaseStageOpponentLawyerAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageOpponentLawyerAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});
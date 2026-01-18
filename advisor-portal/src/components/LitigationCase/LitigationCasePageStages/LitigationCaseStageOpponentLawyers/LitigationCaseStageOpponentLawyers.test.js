import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageOpponentLawyers from './LitigationCaseStageOpponentLawyers';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageOpponentLawyers />, div);
  ReactDOM.unmountComponentAtNode(div);
});
import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageStagesTab from './LitigationCasePageStagesTab';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageStagesTab />, div);
  ReactDOM.unmountComponentAtNode(div);
});
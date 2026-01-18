import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageHeaderActionsToolbar from './LitigationCasePageHeaderActionsToolbar';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageHeaderActionsToolbar />, div);
  ReactDOM.unmountComponentAtNode(div);
});
import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageTasks from './LitigationCasePageTasks';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageTasks />, div);
  ReactDOM.unmountComponentAtNode(div);
});
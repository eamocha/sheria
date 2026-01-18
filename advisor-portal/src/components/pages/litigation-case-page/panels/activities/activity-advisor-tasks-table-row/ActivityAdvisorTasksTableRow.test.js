import React from 'react';
import ReactDOM from 'react-dom';
import ActivityAdvisorTasksTableRow from './ActivityAdvisorTasksTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityAdvisorTasksTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
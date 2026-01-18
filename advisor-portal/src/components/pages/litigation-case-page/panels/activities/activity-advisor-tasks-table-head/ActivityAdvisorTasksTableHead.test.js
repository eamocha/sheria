import React from 'react';
import ReactDOM from 'react-dom';
import ActivityAdvisorTasksTableHead from './ActivityAdvisorTasksTableHead';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityAdvisorTasksTableHead />, div);
  ReactDOM.unmountComponentAtNode(div);
});
import React from 'react';
import ReactDOM from 'react-dom';
import ActivityAdvisorTasksTable from './ActivityAdvisorTasksTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityAdvisorTasksTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});
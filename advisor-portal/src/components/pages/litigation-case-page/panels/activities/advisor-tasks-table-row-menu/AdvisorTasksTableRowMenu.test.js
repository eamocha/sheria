import React from 'react';
import ReactDOM from 'react-dom';
import AdvisorTasksTableRowMenu from './AdvisorTasksTableRowMenu';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AdvisorTasksTableRowMenu />, div);
  ReactDOM.unmountComponentAtNode(div);
});
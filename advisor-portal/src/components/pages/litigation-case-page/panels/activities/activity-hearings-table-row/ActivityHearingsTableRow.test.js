import React from 'react';
import ReactDOM from 'react-dom';
import ActivityHearingsTableRow from './ActivityHearingsTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ActivityHearingsTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});
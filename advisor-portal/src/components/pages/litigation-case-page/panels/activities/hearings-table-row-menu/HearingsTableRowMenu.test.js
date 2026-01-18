import React from 'react';
import ReactDOM from 'react-dom';
import HearingsTableRowMenu from './HearingsTableRowMenu';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<HearingsTableRowMenu />, div);
  ReactDOM.unmountComponentAtNode(div);
});
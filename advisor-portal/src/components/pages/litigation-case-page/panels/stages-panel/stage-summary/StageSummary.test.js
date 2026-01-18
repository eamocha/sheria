import React from 'react';
import ReactDOM from 'react-dom';
import StageSummary from './StageSummary';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageSummary />, div);
  ReactDOM.unmountComponentAtNode(div);
});
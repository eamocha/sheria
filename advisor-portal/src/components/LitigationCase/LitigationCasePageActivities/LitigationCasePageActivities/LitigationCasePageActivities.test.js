import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageActivities from './LitigationCasePageActivities';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageActivities />, div);
  ReactDOM.unmountComponentAtNode(div);
});
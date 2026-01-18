import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewContributors from './LitigationCasePageBasicViewContributors';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewContributors />, div);
  ReactDOM.unmountComponentAtNode(div);
});
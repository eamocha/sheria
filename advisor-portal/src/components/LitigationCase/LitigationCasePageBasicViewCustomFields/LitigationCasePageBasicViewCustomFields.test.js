import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewCustomFields from './LitigationCasePageBasicViewCustomFields';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewCustomFields />, div);
  ReactDOM.unmountComponentAtNode(div);
});
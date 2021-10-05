FROM python:3-slim-buster

WORKDIR /testes
ENV VIRTUAL_ENV=${UHOME}/venv

RUN apt-get update -y && \
    pip3 install --no-cache-dir --upgrade pip && \
    pip3 install --no-cache-dir  \
              robotframework \
              robotframework-requests

ENV PATH="$VIRTUAL_ENV/bin:$PATH"
CMD python3 -m robot --outputdir /report .

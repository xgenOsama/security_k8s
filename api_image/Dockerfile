FROM python:3.6.6-alpine
COPY . /app
WORKDIR /app
RUN pip3 install -r requirements.txt
EXPOSE 80
ENTRYPOINT ["python3"]
CMD ["api.py"]